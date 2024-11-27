#pip install pycups

import socket

# Define a URL da impressora (no formato IPv6)
printer_host = "143.54.112.86:631/printers/ARGOX"
printer_port = 631  # Porta padrão para envio direto de comandos RAW

# Exemplo de comando EPL/ZPL para etiqueta
label_command = """
^XA
^FO50,50^ADN,36,20^FDHello ARGOX^FS
^FO50,100^B3N,N,50,Y,N^FD1234567890^FS
^XZ
"""

try:
    # Cria uma conexão socket com a impressora
    with socket.socket(socket.AF_INET6, socket.SOCK_STREAM) as s:
        s.connect((printer_host, printer_port))
        s.sendall(label_command.encode('utf-8'))
    print("Etiqueta enviada com sucesso!")
except Exception as e:
    print(f"Erro ao enviar etiqueta: {e}")
