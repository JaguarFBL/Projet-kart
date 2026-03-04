import pymysql
connection = pymysql.connect(host='localhost',user='root',password='poteau',database='kart',charset='utf8mb4')

try: 
    with connection.cursor() as cursor:
        cursor.execute("INSERT INTO pilotes (id,nom,record) VALUES (%s, %s, %s)",(4,'Noah',90))
        connection.commit()
        cursor.execute("SELECT * FROM pilotes")
        resultat=cursor.fetchall()
        for row in resultat:
            print(row)
finally:
    connection.close()