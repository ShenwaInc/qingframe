from ultralytics import YOLO

def main():
    # 加载模型
    model = YOLO(r"runs/detect/train/weights/best.pt")
    model.val(data="data_tq.yaml", split='val', imgsz=1024, batch=16, workers=0)  #模型验证
if __name__ == "__main__":
    main()