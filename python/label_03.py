import requests

# URL da impressora (IPP)
printer_url = "http://143.54.112.86:631/printers/ARGOX"

# Comando ou arquivo ZPL/EPL para impressão
label_command = """
^XA
^FO50,50^ADN,36,20^FDHello ARGOX^FS
^FO50,100^B3N,N,50,Y,N^FD1234567890^FS
^XZ
"""

try:
    # Envie o comando para a impressora como uma requisição POST
    response = requests.post(
        printer_url,
        data=label_command.encode('utf-8'),
        headers={"Content-Type": "application/octet-stream"}
    )
    if response.status_code == 200:
        print("Etiqueta enviada com sucesso!")
    else:
        print(f"Erro ao enviar etiqueta: {response.status_code}, {response.text}")
except Exception as e:
    print(f"Erro ao enviar etiqueta: {e}")
