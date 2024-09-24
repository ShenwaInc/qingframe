import matplotlib.pyplot as plt
import matplotlib
matplotlib.use('TkAgg')
from ultralytics import YOLO
import cv2
import sys
import os
import json

# 确保接收的路径参数
if len(sys.argv) != 2:
    print("用法: python script_name.py <图片路径>")
    sys.exit(1)

# 获取图片路径
image_path = sys.argv[1]

# 定义输出文件名和路径
output_file_name = "detection_results.json"
output_dir = os.path.abspath(".")  # 获取当前工作目录的绝对路径
output_file_path = os.path.join(output_dir, output_file_name)

# 加载训练好的模型
model = YOLO(r"./best.pt")

# 进行检测
results = model(image_path, save=True)

# 存储检测结果中的类别
categories = []

# 处理检测结果
for result in results:
    # 获取图片路径
    img_path = result.path
    img = cv2.imread(img_path)
    img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)

    # 绘制检测结果
    plt.imshow(img)
    for box, cls in zip(result.boxes.xyxy, result.boxes.cls):
        x1, y1, x2, y2 = box
        x1, y1, x2, y2 = int(x1), int(y1), int(x2), int(y2)
        cx, cy = (x1 + x2) / 2, (y1 + y2) / 2
        plt.gca().add_patch(plt.Rectangle((x1, y1), x2 - x1, y2 - y1, fill=False, edgecolor='red', linewidth=2))
        plt.scatter(cx, cy, color='blue')  # 绘制中心点
        label = result.names[int(cls)]
        plt.text(x1, y1, label, color='red', fontsize=8, verticalalignment='top')

        # 保存类别和几何中心点坐标
        categories.append((label, (cx, cy)))

    # 按 x 坐标递增的顺序排序
    sorted_categories = sorted(categories, key=lambda item: item[1][0])

    # 构造 JSON 数据
    output_data = {
        "categories_with_coordinates": [
            {"category": label, "coordinates": coords}
            for label, coords in sorted_categories
        ]
    }

    # 输出结果到 JSON 文件
    with open(output_file_path, "w") as f:
        json.dump(output_data, f, indent=4)
    print(f"检测结果已保存到 {output_file_path}")
    # plt.axis('off')
    # plt.show()

