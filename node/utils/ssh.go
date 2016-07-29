package ssh

import (
	"fmt"
	"os/exec"
)

func check(e error) {
	if e != nil {
		panic(e)
	}
}

type SSHCommander struct {
	User string
	IP   string
}

func (s *SSHCommander) Command(cmd ...string) {
	args := append(
		[]string{
			fmt.Sprintf("-p 'vagrant' ssh -o StrictHostKeyChecking=no %s@%s", s.User, s.IP),
		},
		cmd...,
	)
    fmt.Printf("%s\n", args)

	out, err := exec.Command("sshpass", args...).Output()
	check(err)
	fmt.Printf("%s\n", out)
}

func main() {
	commander := SSHCommander{"vagrant", "192.168.33.11"}
	cmd := []string{
		"pwd",
	}
	commander.Command(cmd...)
}
