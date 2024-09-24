import cv2
import socket
import threading
import subprocess
from datetime import datetime

# 摄像头拍照函数
def take_photo(camera_index):
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    photo_filename = f'test_{timestamp}.jpg'
    cap = cv2.VideoCapture(camera_index, cv2.CAP_DSHOW)
    if not cap.isOpened():
        print("无法打开摄像头")
        return
    ret, frame = cap.read()
    if ret:
        cv2.imwrite(photo_filename, frame)
        print(f"照片已保存为{photo_filename}")
    else:
        print("捕获图像失败")
    cap.release()
    return photo_filename

# 套接字服务器函数
def start_server(host, port):
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    server_socket.bind((host, port))
    server_socket.listen(1)
    print(f"Listening on {host}:{port}...")

    while True:
        client_socket, addr = server_socket.accept()
        print(f"Connection from {addr} has been established.")
        try:
            message = client_socket.recv(1024).decode('utf-8')
            if message == 'TAKE_PHOTO':
                print("收到拍照信号，执行拍照...")
                photo_filename = take_photo(0)  # 获取拍照文件名
                if photo_filename:
                    # 调用检测脚本
                    result = subprocess.run(['python', 'detecte.py', photo_filename], capture_output=True, text=True)
                    detection_result = result.stdout
                    print(f"检测结果: {detection_result}")

                    # 发送检测结果回客户端
                    client_socket.sendall(detection_result.encode('utf-8'))
                else:
                    client_socket.sendall("拍照失败".encode('utf-8'))
            else:
                client_socket.sendall("无效信号".encode('utf-8'))
        finally:
            client_socket.close()

# 启动套接字服务器的线程
host = '127.0.0.1'  # 本地主机地址
port = 12345  # 非常用端口号

# 创建并启动线程
server_thread = threading.Thread(target=start_server, args=(host, port))
server_thread.daemon = True
server_thread.start()

# 等待服务器线程结束（在这个例子中，它不会结束，除非您手动停止它）
server_thread.join()
