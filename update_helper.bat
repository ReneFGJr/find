echo off
echo "Language"
mkdir app\Language\pt-BR
copy ..\Brapci3.1\app\Language\pt-BR\social.* app\Language\pt-BR\*.*

echo "Copiando Helper"
copy ..\Brapci3.1\app\Helpers\*.* app\Helpers\*.*
copy ..\Brapci3.1\app\Models\Social*.* app\Models\*.*

echo "RDP"
mkdir app\Models\Rdf

echo "AI"
mkdir app\Models\AI
mkdir app\Models\AI\NLP
copy ..\Brapci3.1\app\Models\AI\NLP\*.php app\Models\AI\NLP\*.*

echo "RDF"
copy ..\Brapci3.1\app\Models\RDF\RDF*.php app\Models\RDF\*.*

echo "Images"
copy ..\Brapci3.1\app\Models\Images.php app\Models\*.*

echo "IO"
mkdir app\Models\Io
copy ..\Brapci3.1\app\Models\Io\*.php app\Models\Io\*.*
