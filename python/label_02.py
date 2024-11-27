import cups

# Conecte ao servidor CUPS
conn = cups.Connection()

# Localize a impressora
printer_name = "http://143.54.112.86:631/printers/ARGOX"

# Envie o arquivo de impressão
label_file = "label.zpl"
conn.printFile(printer_name, label_file, "Etiqueta", {})
