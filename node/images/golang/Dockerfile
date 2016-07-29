# COPY . /go/src/app
# RUN go get -d -v
# RUN go install -v
FROM golang:1.6-onbuild

# OR (WITHOUT ONBUILD):
# FROM golang:1.6

# USAGE
# docker build -t wshell-golang .

# typical run
# docker run --rm wshell-golang

# mount volume for build unit (cache dir)
# docker run --rm -v "$PWD"/cache/wshell-golang/TESTHASH:/usr/src/myapp -w /usr/src/myapp wshell-golang go build -v

# compilation node
# docker run --rm -v "$PWD":/usr/src/myapp -w /usr/src/myapp wshell-golang go build -v
