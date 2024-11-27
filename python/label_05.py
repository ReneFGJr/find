import requests

# Configuração da impressora
printer_url = "http://143.54.112.86:631/printers/ARGOX"

# Comando ZPL/EPL para a etiqueta
label_command = """
^XA
^FO50,50^ADN,36,20^FDHello ARGOX^FS
^FO50,100^B3N,N,50,Y,N^FD1234567890^FS
^XZ
"""

# Headers necessários para o IPP
headers = {
    "Content-Type": "application/octet-stream"
}

try:
    # Enviar o comando ZPL/EPL para a impressora
    response = requests.post(printer_url, data=label_command.encode('utf-8'), headers=headers, verify=False)

    # Verificar a resposta
    if response.status_code == 200:
        print("Etiqueta enviada com sucesso!")
    else:
        print(f"Erro ao enviar etiqueta. Código: {response.status_code}, Resposta: {response.text}")
except Exception as e:
    print(f"Erro ao enviar etiqueta: {e}")
