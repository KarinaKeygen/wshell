# coding: utf-8

import sys
import os

path = os.path.dirname(__file__)
sys.path.append(path)
os.chdir(path)

def application(environ, start_response):
    status = '200 OK'
    output = 'Hello uWSGI!!!'

    response_headers = [('Content-type', 'text/html; charset=utf-8'),
                        ('Content-Length', str(len(output)))]
    start_response(status, response_headers)

    return [output]