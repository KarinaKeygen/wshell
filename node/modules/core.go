package node

import (
	"os"
	"os/exec"
	"encoding/json"
	"encoding/base64"
	"bytes"
	"log"
	"fmt"
	// "crypto/md5"
	"strings"
	"regexp"
)

func Check(e error) {
	if e != nil {
		if e.Error() != "" {
			log.Println(e.Error())
			os.Exit(1)
		}
		panic(e)
	}
}

func Cmd(cmdName string, cmdArgs []string) string {
	cmd := exec.Command(cmdName, cmdArgs...)

	var out bytes.Buffer
	var stderr bytes.Buffer
	cmd.Stdout = &out
	cmd.Stderr = &stderr
	err := cmd.Run()
	if err != nil {
		log.Println("CMD ERROR> " + fmt.Sprint(err) + ":" + stderr.String())
		// os.Exit(1)
	}
	return out.String()
}

//	Core config structures
type WampConfiguration struct {
	Url string
	Realm string
	Topics map[string]string
}
type Configuration struct {
    Role string
    Path string
    VotingExpire string
    Wamp WampConfiguration
    CzmqAddr string
    Debug bool
}
// Unit metadata
type MetaData struct {
    Name string
    Hookup string
}
/*
	n - network
	t - tasks
	d - docker
	i - czmq interactioon
*/
type NodeCore struct {
	Config Configuration
	Done chan bool

	inbox chan [][]byte
	outbox chan [][]byte
	nToT chan [][]byte
	tToN chan [][]byte
	tToD chan [][]byte
	dToT chan [][]byte

	Dm DockerManager
	Tm TaskManager
	Nm NetworkManager
}

// public
func (core *NodeCore) Init(confPath string) {
	// check if root
	if os.Getgid() != 0 {
		log.Println("Sorry, need root permission")
		os.Exit(1)
	}

	// load config
	file, err := os.Open(confPath)
	Check(err)
	decoder := json.NewDecoder(file)
	configuration := Configuration{}
	err = decoder.Decode(&configuration)
	Check(err)
	core.Config = configuration

	// init channels for network interaction
	core.inbox = make(chan [][]byte)
	core.outbox = make(chan [][]byte)

	// init channels for modules interaction
	core.nToT = make(chan [][]byte)
	core.tToN = make(chan [][]byte)
	core.tToD = make(chan [][]byte)
	core.dToT = make(chan [][]byte)

	// init finish flag
	core.Done = make(chan bool, 0)
}

func (core *NodeCore) RunDockerModule() {
	if !core.Dm.CheckDockerDaemon() {
		log.Println("DM: Docker daemon not running")
		os.Exit(1)
	}

	go func() {
		for {
			// parse unit from task module
			unit := <-core.tToD
			imageName  := string(unit[0])
			program    := string(unit[1])
			entryPoint := string(unit[2])
			unitName   := string(unit[3])
			hookup     := string(unit[4])
			unitDataFile := string(unit[5])

			containerRunOptions := core.Dm.DefaultContainerOptions()
			containerRunOptions["-v"] = core.Config.Path + "/cache/" + imageName + ":/usr/src/myapp/cache"
			containerRunOptions["-w"] = "/usr/src/myapp"

			// empty if require create new container
			containerId := core.Dm.GetExistContainer(imageName)

			// foreground run container before exec
			if containerId == "" {
				fCommand := "/bin/sh"
				// 60 sec - time limit for execute
				fArgs := []string{"-c", "sleep 60;"}
				// get short 12-byte containerId
				containerId = core.Dm.Run(containerRunOptions, imageName, fCommand, fArgs)[0:12]
				log.Println("Run container with ID:", containerId)
			}

			mByteData, _ := json.Marshal( MetaData{unitName, hookup} )
			mData := base64.StdEncoding.EncodeToString(mByteData)

			// EXECUTE UNIT
			result := core.Dm.Exec(containerId, program, []string{entryPoint, mData, unitDataFile })

			log.Printf("DM: Success execute in container %s\n", containerId)
			core.dToT <- [][]byte{ []byte(result) }
		}
	}()
}

func (core *NodeCore) RunNetworkModule() {
	// implement Publish/Subscribe as core.inbox/core.outbox
	core.Nm.CreateWampClient(*core)
	// from network to task
	go func() {
	  for {
			task := <-core.inbox
			log.Println("NM: Received task from wamp")
			core.nToT <- task
		}
	}()
	// from task to network
	go func() {
	  for {
			message := <-core.tToN
			log.Println("NM: Send result to wamp")
			core.outbox <- message
		}
	}()
}

func (core *NodeCore) RunTasksModule() {
	// from network to task
	go func() {
	    for {
		    message := <-core.nToT
		    log.Printf("TM: Message recieved")
			// []Units + clientId
			unitBlock := core.Tm.Parse(message)
			log.Println("TM: unitBlock parsed")

			core.Tm.SaveToCache(unitBlock) // cache/inbox/clientId : units
			log.Printf("TM: unitBlock saved in cache")

			log.Printf("TM: Run voting...")
			// TODO: ITS NETWORK helper. [unit_id, wamp_id, container_id}
			// uses "index","order" and "require"
			// voting happens, if votingTimer > votingTimerLimit
			// for local -> wamp_id is empty
			// nodes := voting(UnitBlock.Units)
			log.Printf("TM: Nodes finding!")

			// prepare images list for local run units
			imagesInfoList := strings.Split(core.Dm.Info("images"), "\n")
			imagesList := map[string]string{}
			for _, imageInfo := range imagesInfoList {
				words := regexp.MustCompile("[\t ]{2,}").Split(imageInfo, 5)
				// find only *-wshell images
				if len(words) > 1 && len(words[0]) > 7 && words[0][len(words[0])-7:] == "-wshell" {
					imagesList[words[2]] = words[0]
				}
			}
			log.Println("TM: ImagesList for current unitBlock:", imagesList)

			// TODO: range Nodes
			for _, unit := range unitBlock.Units {

				// find image for unit
				// TODO: require not only image name
				findImage := []string{}
				for imageId, name := range imagesList {
					if (unit.Require[0] + "-wshell") == name {
						findImage = []string{imageId, name}
					}
				}
				if len(findImage) == 0 {
					log.Println("TM: Unable find image ", unit.Require[0] + "-wshell")
					os.Exit(1)
				}

				nodeId := []byte(nil) // if local exec
				imageName := []byte(findImage[1])

				log.Println("TM: parse Unit.args")

				argBlock := []byte{}
				if len(unit.Args) > 0 {
					argBlock, _ = json.Marshal(unit.Args)
				}
				// TODO: prepare ChainArgs with defers!
				// if len(unit.ChainArgs) > 0 {
				// 	argBlock, _ = json.Marshal(unit.Args)
				// }

				// argBlock := core.Tm.CreateArgBlock(unit)
				log.Println("TM: create argBlock")

				// hash := []byte(fmt.Sprintf("%x", md5.Sum([]byte(unit.Source))))
				unitName := []byte(unit.Name)
				unitHookup := []byte(unit.Hookup)

				// TODO: remove code specify for image
				command := []byte(nil)
				entryPoint := []byte(nil)
				if unit.Require[0] == "php" {
					command = []byte("php")
					entryPoint = []byte("main.php")
				}
				if unit.Require[0] == "python" {
					command = []byte("python")
					entryPoint = []byte("main.py")
				}
				if unit.Require[0] == "go" {
					command = []byte("go")
					entryPoint = []byte("main.go")
				}

				// local exec
				if string(nodeId) == "" {
					// save in cache unitSource
					path := core.Config.Path + "/cache/" + string(imageName)
					core.Tm.SaveToVolume(path, string(unitName), unit.Source)
					// save in cache unitData
					unitDataFile := string(unitName) + "_data"
					unitData := base64.StdEncoding.EncodeToString(argBlock)
					core.Tm.SaveToVolume(path, unitDataFile, unitData)

					log.Printf("TM: Unit run locally")
					// create imageDir and unitFile
					unit := [][]byte{ imageName, command, entryPoint, unitName, unitHookup, []byte(unitDataFile) }
					core.tToD <- unit
				} else {
					log.Printf("TM: Unit send to node %s", nodeId)
					unit := [][]byte{ nodeId, imageName, command, entryPoint, unitName, unitHookup, argBlock }
					core.tToN <- unit
				}
			}
	    }
    }()

	// from task to network
	go func() {
			for {
				message := <-core.dToT
				log.Println("TM: Result message:", string(message[0]))
				// TODO: save to task queue
				core.outbox <- message
			}
	}()
}
