yum install -y wget
wget https://studygolang.com/dl/golang/go1.14.1.linux-amd64.tar.gz
tar -C /usr/local -xzf go1.14.1.linux-amd64.tar.gz
vim /etc/profile export GOROOT=/usr/local/go
export GOPATH=D:/wamp/www/whotalklar/socket
export PATH=$PATH:$GOROOT/bin:$GOPATH/bin

go mod init xfy_whotalk_socket
go mod tidy
go mod vendor

go run main.go

rm -f go1.14.1.linux-amd64.tar.gz