package node

import (
	"log"
	"strings"
)

// TODO: replace on https://github.com/fsouza/go-dockerclient

type DockerManager struct {
}

func (dm *DockerManager) CheckDockerDaemon() bool {
	args := []string{"docker", "status"}
	out := Cmd("service", args)

	// example out: docker start/running, process 1324
	// find and Check running status
	Check := false
	if strings.Contains(out, "running") {
		Check = true
	}
	return Check
}

func (dm *DockerManager) GetExistContainer(imageName string) string {
	containerId := ""

	args := []string{"ps", "-a"}
	containers := strings.Split(Cmd("docker", args), "\n")
	for container := range containers {
		if strings.Contains(containers[container], imageName) {
			fields := strings.Fields(containers[container])
			containerId = fields[0]
			log.Println("DM: Finded container:", containerId)
			running := strings.TrimSpace(dm.ContainerInfo(containerId, "{{.State.Running}}"))
			log.Println("DM: container running:", running)

			if running == "false" {
				dm.Start(containerId)
				log.Println("DM: Container was running")
			}
			break;
		}
	}

	return string(containerId)
}

func (dm *DockerManager) Info(info string) (result string) {
	args := []string{}
	switch info {
	case "version":
		args = []string{"version"}
	case "system":
		args = []string{"info"}
	case "images":
		args = []string{"images"}
	case "containers":
		args = []string{"ps", "-a"}
	}
	result = Cmd("docker", args)
	return result
}

func (dm *DockerManager) ContainerInfo(id string, selector string) string {
	args := []string{"inspect"}
	if selector != "" {
		args = append(args, "-f", selector)
	}
	args = append(args, id)
	out := Cmd("docker", args)
	return out
}

func (dm *DockerManager) Run(options map[string]string, image string, command string, cArgs []string) (containerId string) {
	// merge all args
	args := []string{"run"}
	for optionName, option := range options {
		args = append(args, optionName+"="+option)
	}
	args = append(args, image)
	if command != "" {
		args = append(args, command)
	}
	for _, arg := range cArgs {
		args = append(args, arg)
	}
	log.Println("Container run with args:", args)
	out := Cmd("docker", args)
	return out
}
func (dm *DockerManager) Exec(id string, command string, cArgs []string) (result string){
	args := []string{"exec"}
	args = append(args, id)
	if command != "" {
		args = append(args, command)
	}
	for _, arg := range cArgs {
		args = append(args, arg)
	}
	// debug
	log.Println("Container exec with args:", args)
	result = Cmd("docker", args)
	return result
}

func (dm *DockerManager) Diff(id string) {
	args := []string{"diff", id}
	out := Cmd("docker", args)
	log.Println(out)
}
func (dm *DockerManager) Top(id string) {
	args := []string{"top", id}
	out := Cmd("docker", args)
	log.Println(out)
}
func (dm *DockerManager) Restart(id string) {
	args := []string{"restart", id}
	out := Cmd("docker", args)
	log.Println(out)
}
func (dm *DockerManager) Pause(id string, pause bool) {
	args := []string{}
	if pause {
		args = []string{"pause", id}
	} else {
		args = []string{"unpause", id}
	}
	out := Cmd("docker", args)
	log.Println(out)
}
func (dm *DockerManager) Stop(id string) string {
	out := Cmd("docker", []string{"stop", id})
	return out
}
func (dm *DockerManager) Start(id string) string {
	out := Cmd("docker", []string{"start", id})
	return out
}
func (dm *DockerManager) Remove(id string) {
	args := []string{"rm", id}
	out := Cmd("docker", args)
	log.Println(out)
}

// sudo docker stop $(sudo docker ps -a -q)
// sudo docker rm $(sudo docker ps -a -q)
func (dm *DockerManager) Reset() {
	// get list
	args := []string{"ps", "-a", "-q"}
	containers := strings.Split(Cmd("docker", args), "\n")
	// delete last, empty string
	containers = containers[:len(containers)-1]

	args = []string{"stop"}
	for _, id := range containers {
		args = append(args, id)
	}
	if len(args) != 1 {
		log.Println("Stop containers:")
		out := Cmd("docker", args)
		log.Println(out)
	}

	args = []string{"rm"}
	for _, id := range containers {
		args = append(args, id)
	}
	if len(args) != 1 {
		log.Println("Remove containers:")
		out := Cmd("docker", args)
		log.Println(out)
	}
}

func (dm *DockerManager) DefaultContainerOptions() (map[string]string) {
	// * detached or foreground running
	// * container identification
	// * network settings
	// * runtime constraints on CPU and memory
	// * privileges and LXC configuration
	options := map[string]string {
		"--detach": "true",
		// "-it": "false", //interactive
		// "--name": "test",
		// "--hostname": "testhost",
		// "--cidfile": "/tmp/docker_test.cid",
		//  // true enable "danger" core functions (контейнер может всё, что и хост)
		// "--privileged": "false",
		//
		// // "sandbox" dir mount as /sandbox and run in /sandbox
		// "--workdir": "/sandbox",
		// "--volume": "sandbox:/sandbox",
		//
		// // "--mac-address",
		// "-p": "127.0.0.1:80:8080", // connect TO container
		// "--add-host": "main:192.168.0.1", // connect FROM container TO host by host name
		// "--env-file": "env.list",
		// "--net": "multi-container-network",
		//
		// "--rm": "true", // temp
		// "--user": "oleg:oleg", // <name|uid>[:<group|gid>]
		//
		// // https://docs.docker.com/engine/reference/run/#runtime-constraints-on-resources
		// "--ulimit": "nofile=1024:1024" // soft and hard limits
		// "--memory": "16M", // RAM (b,k,m,g)
		// "--kernel-memory": "16M", // core-space RAM ?
		// "--cpuset-shares": "62", доля прооцессогрного времени (макс 1024)
		// "--cpuset-cpus": "0-3",
		// "--cpuset-mems": "0-3", //Memory nodes (MEMs) Only effective on NUMA systems

		// https://docs.docker.com/engine/reference/run/#clean-up-rm
		// "--security-opt=[]",
		// --security-opt="label:user:USER"   : Set the label user for the container
		// --security-opt="label:role:ROLE"   : Set the label role for the container
		// --security-opt="label:type:TYPE"   : Set the label type for the container
		// --security-opt="label:level:LEVEL" : Set the label level for the container
		// --security-opt="label:disable"     : Turn off label confinement for the container
		// --security-opt="apparmor:PROFILE"  : Set the apparmor profile to be applied
	}
	return options
}


// utils
func allTest() {
	var dm DockerManager

	// TODO: set info about current images other
	// dm.Info("version")
	// dm.Info("system")
	// dm.Info("images")
	// dm.Info("containers")
	// dm.ContainerInfo(containerId)

	// many usefull info:
	// Id Created Args State RestartCount Mounts ExecIDs HostConfig Config NetworkSettings
	// containerId := "0d42b8b75244"
	// dm.ContainerInfo(containerId)

	// helper: stop and remove all containers
	// TODO: bug with foreground containers
	// dm.Reset()

	// helper: stop and remove all containers
	dm.Reset()

	command := ""
	options := dm.DefaultContainerOptions()
	containerId := dm.Run(options, "wshell-golang", command, []string{})[0:12]

	dm.Diff(containerId)
	dm.Top(containerId)

	log.Println("Container restarts:")
	dm.Restart(containerId)

	log.Println("Pause/Unpause:")
	dm.Pause(containerId, true)
	dm.Pause(containerId, false)
	log.Println("Stop/Start:")
	dm.Stop(containerId)
	dm.Start(containerId)

	log.Println("Container removes:")
	dm.Remove(containerId)

	// TODO
	// Note: Containers on the default bridge network must be linked to communicate by name.
	// networkName := dm.CreateNetwork()

	/*
		// logs ????
		TODO image api:
		db.build(Dockerfile)
		db.commit(containerID) - save state containers as new image +++
		db.load(image.tar) - load from archive
		db.save(imageID)   - save to archive
		db.remove(imageID)   - save to archive
	*/
}
