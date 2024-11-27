################ pip install pywin32
import win32print
import win32ui

#printer_name = "\\\\[fe80::e2d5:5eff:fea3:eef4]\\printers\\ARGOX"
printer_name = "ARGOX OS-214 plus PPLA 203dpi @ ufrgs432194"
label_command = """
^XA
^FO50,50^ADN,36,20^FDHello ARGOX^FS
^FO50,100^B3N,N,50,Y,N^FD1234567890^FS
^XZ
"""

try:
    # Abre a conexão com a impressora
    hprinter = win32print.OpenPrinter(printer_name)
    hjob = win32print.StartDocPrinter(hprinter, 1, ("Etiqueta", None, "RAW"))
    win32print.StartPagePrinter(hprinter)
    win32print.WritePrinter(hprinter, label_command.encode('utf-8'))
    win32print.EndPagePrinter(hprinter)
    win32print.EndDocPrinter(hprinter)
    win32print.ClosePrinter(hprinter)
    print("Etiqueta enviada com sucesso!")
except Exception as e:
    print(f"Erro ao imprimir etiqueta: {e}")
