@echo off
echo "Copiando Helper"
copy ..\Brapci3.0\app\Helpers\*.* app\Helpers\*.*
copy ..\Brapci3.0\app\Models\Social*.* app\Models\*.*

echo "RDP"
copy ..\Brapci3.0\app\Models\RDF.php app\Models\*.*
copy ..\Brapci3.0\app\Models\RDFConcept.php app\Models\*.*

RDFConcept