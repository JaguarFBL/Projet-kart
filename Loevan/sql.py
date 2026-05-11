
from flask import Flask, render_template, request, jsonify

import pymysql


import asyncio
from bleak import BleakClient, BleakScanner
MAC = "A5:C2:37:47:DB:04"
UUID_RX = "0000ff01-0000-1000-8000-00805f9b34fb"  # notification donn�es
UUID_TX = "0000ff02-0000-1000-8000-00805f9b34fb"  # �criture commandes
# Commandes JBD standard
CMD_BASIC   = bytes([0xDD, 0xA5, 0x03, 0x00, 0xFF, 0xFD, 0x77])  # tension, SOC, courant...
CMD_CELLS   = bytes([0xDD, 0xA5, 0x04, 0x00, 0xFF, 0xFC, 0x77])  # tensions par cellule
response_data = bytearray()
def notification_handler(sender, data):
   global response_data
   response_data += data
   print(f"Re�u: {data.hex()}")
def decode_basic(data):
   if len(data) < 20:
       return
   global voltage
   global current
   global soc
   global temp1

   voltage    = int.from_bytes(data[4:6],  'big') / 100.0
   current    = int.from_bytes(data[6:8],  'big', signed=True) / 100.0
   soc        = data[23]
   temp1      = (int.from_bytes(data[27:29], 'big') - 2731) / 10.0
   
   print(f"\n=== BATTERIE ===")
   print(f"Tension totale : {voltage} V")
   print(f"Courant        : {current} A")
   print(f"SOC            : {soc} %")
   print(f"Temp�rature    : {temp1} �C")
async def read_battery():
   scanner = BleakScanner()
   await scanner.start()
   await asyncio.sleep(8)
   await scanner.stop()
   target = next((d for d in scanner.discovered_devices
                  if d.address.upper() == MAC.upper()), None)
   if not target:
       print("Batterie non trouv�e")
       return
   async with BleakClient(target, timeout=30.0) as client:
       print("Connect� !")
       # Activer les notifications sur ff01
       await client.start_notify(UUID_RX, notification_handler)
       # Envoyer commande donn�es de base
       response_data.clear()
       await client.write_gatt_char(UUID_TX, CMD_BASIC, response=False)
       await asyncio.sleep(1)
       print(f"\nR�ponse brute: {response_data.hex()}")
       decode_basic(response_data)
       # Commande tensions cellules
       response_data.clear()
       await client.write_gatt_char(UUID_TX, CMD_CELLS, response=False)
       await asyncio.sleep(1)
       print(f"\nCellules brut: {response_data.hex()}")
       await client.stop_notify(UUID_RX)
asyncio.run(read_battery())

app= Flask(__name__)

temperaturepiste=None
humiditepiste= None



@app.route('/session', methods=['POST'])
def receive_data_session():
    donnee=request.get_json()
    print(donnee)
    global temperaturepiste
    global humiditepiste
    connection = pymysql.connect(host='localhost',user='root',password='poteau',database='kart',charset='utf8mb4')
    temperaturepiste=donnee['valeurtemp']
    humiditepiste=donnee['valeurhumid']
    timestamp=donnee['timestamp_ms']

    
    with connection.cursor() as cursor:
       # R�cup�rer le pilote actif
       cursor.execute("INSERT INTO capteur (temperaturebatterie,intensitebatterie,tensionbatterie,pourcentagebatterie) VALUES (%s,%s,%s,%s)",(temp1,current,voltage,soc)) 
       cursor.execute("SELECT pilote FROM actif")
       pilote = cursor.fetchone()
       if pilote is None:
           return "Aucun pilote actif", 400
       pilote = pilote[0]
       # R�cup�rer le timestamp du dernier tour de ce pilote
       cursor.execute(
           "SELECT timestamp FROM session WHERE pilote = %s ORDER BY ID DESC LIMIT 1",
           (pilote,)
       )
       dernier = cursor.fetchone()
       if dernier is None:
           # Premier tour : pas de temps calculable
           temps = 0
       else:
           temps = timestamp - dernier[0]
       # Ins�rer le nouveau passage
       cursor.execute(
           "INSERT INTO session (pilote, timestamp, temps) VALUES (%s, %s, %s)",
           (pilote, timestamp, temps)
       )

       
    connection.commit()
    connection.close()
    return "OK"


@app.route('/capteur', methods=['POST'])
def receive_data_capteur():
    donnee=request.get_json()
    print(donnee)

    connection = pymysql.connect(host='localhost',user='root',password='poteau',database='kart',charset='utf8mb4')
    

    temperaturebatterie=donnee['temperaturebatterie']
    
    intensitebatterie=donnee['intensitebatterie']
    global voltage
    global current
    global soc
    global temp1
    with connection.cursor() as cursor:
        if temperaturepiste is not None :
            cursor.execute("INSERT INTO capteur (temperaturebatterie,intensitebatterie,temperaturepiste,humiditepiste,tensionbatterie,pourcentagebatterie) VALUES (%s,%s,%s,%s,%s,%s)",(temp1,current,temperaturepiste,humiditepiste,voltage,soc)) 
        
    connection.commit()
    connection.close()
    return "OK"


if __name__ == "__main__":
    print("demarrage ok")
    app.run(host='0.0.0.0',port=5000)
    print("fin")





    
   