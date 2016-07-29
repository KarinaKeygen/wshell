package main

import (
	"github.com/zeromq/goczmq"
	"log"
)

func check(e error) {
	if e != nil {
		panic(e)
	}
}

func main() {

    dealer := goczmq.NewDealerChanneler("tcp://127.0.0.1:5555")
    defer dealer.Destroy()

    message := "привет мир!"
    log.Println("dealer created and connected")
    dealer.SendChan <- [][]byte{[]byte(message)}
    log.Println("dealer sent " + message)

    reply := <-dealer.RecvChan
    log.Printf("dealer received '%s'", reply[0])
}
