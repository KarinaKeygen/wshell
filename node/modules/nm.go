package node

import (
	"gopkg.in/jcelliott/turnpike.v2"
	"log"
)

type message struct {
	From string
	Message string
}

type NetworkManager struct {
}

func (nm *NetworkManager) CreateWampClient(core NodeCore) {
	conf := core.Config.Wamp

	if core.Config.Debug {
		turnpike.Debug()
	}
	client, err := turnpike.NewWebsocketClient(turnpike.JSON, conf.Url, nil)
	Check(err)
	_, err = client.JoinRealm(conf.Realm, nil)
	Check(err)

	// subscribe on "main" - topic for getting tasks
	client.Subscribe(conf.Topics["main"], func(args []interface{}, kwargs map[string]interface{}) {
		message := args[0].(string)
		// log.Print(message)
		core.inbox <- [][]byte{[]byte(message)}
	})

	go func() {
	    for {
			// result in JSON string
			result := <-core.outbox
			stringResult := string(result[0][:])
			// log.Print(stringResult)
			if len(result) == 6 {
				log.Printf("wamp: Send unit to node: %s\n", result[0])
				message := message{From: "currentNode", Message: stringResult}
				client.Publish(string(result[0]), []interface{}{message.From, message.Message}, nil)
			} else {
				log.Printf("wamp: Send result to client\n")
				message := message{From: "executorID", Message: stringResult}
				client.Publish(conf.Topics["result"], []interface{}{message.From, message.Message}, nil)
			}
		}
	}()
}

// func clasterInfoRequest(client *turnpike.Client, ips []string) {
// 	conf := core.Config.Wamp
// 	client.Publish(conf.Topics["claster"], []interface{}{msg.From, msg.Message}, nil)
// }
//
// func clasterInfoResponse(client *turnpike.Client, ipMaster string) {
// 	conf := core.Config.Wamp
// 	client.Publish(conf.Topics["claster"], nil, nil)
// }
