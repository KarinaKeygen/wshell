package node
//
// import (
// 	"github.com/zeromq/goczmq" // https://godoc.org/github.com/zeromq/goczmq
// 	"fmt"
// 	"log"
// 	"time"
// )
//
// type DaemonProvider interface {
// 	Start(string, string)
// 	WaitConnectDaemons(int)
// }
// type ZmqProvider struct {
// 	router, dealer     *goczmq.Channeler
// 	// main dealer channels
// 	external, internal chan [][]byte
// 	dealerList map[int]int
// }
//
// func (zp *ZmqProvider) Start(addrRouter string) {
//
// 	zp.external = make(chan [][]byte)
// 	zp.internal = make(chan [][]byte)
//
// 	// router - Master Of Daemons
// 	go func() {
// 		zp.router = goczmq.NewRouterChanneler(addrRouter)
// 		defer zp.router.Destroy()
//
// 		for {
// 			request := <-zp.router.RecvChan
// 			log.Println("ZP-router> request: \n", request)
//
// 			// set id to dealerList if not exists
//
// 			// send all
// 			// request only by senderID. TODO: send many!
// 			zp.router.SendChan <- [][]byte{request[0], request[1]}
// 		}
// 	}()
//
// 	// access to zmq
// 	go func() {
// 		zp.dealer = goczmq.NewDealerChanneler(addrRouter)
// 		defer zp.dealer.Destroy()
//
// 		// proxing external channel (only from WAMP)
// 		for {
// 			external := <-zp.external
// 			log.Println("ZP-dealer> external message: \n", external)
// 			zp.dealer.SendChan <- external
// 			// wait answer and receive result
// 			internal := <-zp.dealer.RecvChan
// 			log.Println("ZP-dealer> internal message: \n", internal)
// 			zp.internal <- internal
// 		}
// 	}()
// }
// func (zp *ZmqProvider) WaitConnectDaemons(waitSeconds int) {
// 	fmt.Printf("Wait daemons %d seconds...\n", waitSeconds)
// 	select {
// 	case msg := <-zp.internal:
// 		// TODO: check type message is PING!
// 		log.Println("Daemon connected:", msg)
// 	case <-time.After(time.Duration(waitSeconds) * time.Second):
// 		log.Println("Daemons do not answer\n")
// 	}
// }
//
// func runCzmqBroker(messages chan []byte) {
// 	var zp ZmqProvider
// 	// adress for binding router and connect dealer
// 	zp.Start("tcp://127.0.0.1:5555")
// 	zp.WaitConnectDaemons(3)
// 	// zp.internal and zp.external - is dealer cnannels
//
// 	go func() {
// 		for {
// 			data := <-zp.internal
// 			fmt.Printf("execute result: %s\n", data)
// 			zp.external <- data
// 		}
// 	}()
//
// 	for {
// 		data := <-messages
// 		// fmt.Printf("wshell.execute.test: %s\n", data)
// 		zp.external <- [][]byte{[]byte(data)}
// 	}
// }
