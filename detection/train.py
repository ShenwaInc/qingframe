from ultralytics import YOLO
import os
os.environ['WANDB_MODE'] = 'dryrun'
# 加载模型
model = YOLO("yolov8.yaml")  # 从头开始构建新模型
model = YOLO("weights/yolov8s.pt")  # 加载预训练模型（建议用于训练）
if __name__ == '__main__':
    model.train(data="data_tq.yaml", imgsz=1024, batch=32, epochs=300, workers=0)  # 训练模型
