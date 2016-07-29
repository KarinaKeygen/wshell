package main

import (
	"log"
	"github.com/zeromq/goczmq"
)

func main() {
	done := make(chan bool, 1)

    sub := goczmq.NewSubChanneler("tcp://127.0.0.1:5555", "test")
    defer sub.Destroy()

	pub := goczmq.NewPubChanneler("tcp://127.0.0.1:5555")
    defer pub.Destroy()

    go func() {
	    for {
		    reply := <-sub.RecvChan
		    log.Printf("sub received %s", reply[1])
	    }
    }()

    go func() {
	    for {
	        message := "привет мир!"
		    pub.SendChan <- [][]byte{[]byte("test"), []byte(message)}
    		log.Printf("pub send " + message)
	    }
    }()

    <-done
}
