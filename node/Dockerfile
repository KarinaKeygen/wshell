FROM golang:1.6

ADD modules /go/src/github.com/pilot114/node/modules
RUN go get gopkg.in/jcelliott/turnpike.v2

# build and compilation node
# docker build -t wshell-node . && docker run --rm -v "$PWD":/usr/src/node -w /usr/src/node wshell-node go build -v
