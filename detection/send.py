import socket


def send_photo_command(host, port, message):
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.connect((host, port))
        s.sendall(message.encode('utf-8'))

        # 接收服务器的响应
        response = s.recv(1024).decode('utf-8')
        print(f"Received: {response}")


if __name__ == "__main__":
    host = 'localhost'
    port = 12345
    message = 'TAKE_PHOTO'
    print(f"Send: {message}")
    send_photo_command(host, port, message)
