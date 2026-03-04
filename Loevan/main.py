

from flask import Flask, render_template, request, jsonify
import pymysql

app= Flask(__name__)

@app.route('/', methods=['POST'])
def receive_data():
    donnee=request.data.decode('utf-8')
    print(donnee)
    return "OK"

if __name__ == "__main__":
    print("demarrage ok")
    app.run(host='0.0.0.0',port=5000)
    print("fin")

