from flask import Flask, render_template, request, jsonify

import pymysql


app= Flask(__name__)

@app.route('/', methods=['POST'])
def receive_data():
    donnee=request.get_json()
    print(donnee)

    connection = pymysql.connect(host='localhost',user='root',password='poteau',database='kart',charset='utf8mb4')
    temperaturepiste=donnee["valeurtemp"]
    humiditepiste=donnee["valeurhumid"]
    temperaturebatterie=0
       
    with connection.cursor() as cursor:
        cursor.execute("UPDATE capteur SET valeur=%s WHERE nom=%s",(temperaturepiste,"temperature"))
        cursor.execute("UPDATE capteur SET valeur=%s WHERE nom=%s",(humiditepiste,"humidite"))
        connection.commit()
        print("ok")


    return "OK" 

if __name__ == "__main__":
    print("demarrage ok")
    app.run(host='0.0.0.0',port=5000)
    print("fin")


