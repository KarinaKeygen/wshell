from socket import *
from binascii import hexlify, unhexlify
s = socket(AF_PACKET, SOCK_RAW)
s.bind(("eth0", 0))

dump = "/x90/x94/xe4/x3b/xf1/x4a/x44/x6d/x57/xea/xb8/x7f/x08/x00/x45/x00/x00/x3c/x68/xa8/x00/x00/x80/x01/x6f/x93/xc0/xa8/x00/x25/xd5/xb4/xcc/x03/x08/x00/x4d/x57/x00/x01/x00/x04/x61/x62/x63/x64/x65/x66/x67/x68/x69/x6a/x6b/x6c/x6d/x6e/x6f/x70/x71/x72/x73/x74/x75/x76/x77/x61/x62/x63/x64/x65/x66/x67/x68/x69/x"

s.send(dump)