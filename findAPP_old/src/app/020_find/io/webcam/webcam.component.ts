import { CommonModule } from '@angular/common';
import { Component, ElementRef, ViewChild } from '@angular/core';

@Component({
  selector: 'app-io-webcam',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './webcam.component.html',
  styleUrl: './webcam.component.scss',
})
export class WebcamComponent {
  @ViewChild('videoElement', { static: false })
  videoElement!: ElementRef<HTMLVideoElement>;
  @ViewChild('canvas', { static: false })
  canvas!: ElementRef<HTMLCanvasElement>;
  streaming: boolean = false;
  capturedImage: string | null = null;

  constructor() {}

  ngOnInit(): void {
    this.startWebcam();
  }

  async startWebcam(): Promise<void> {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: true,
        });
        this.videoElement.nativeElement.srcObject = stream;
        this.videoElement.nativeElement.play();
        this.streaming = true;
      } catch (error) {
        console.error('Erro ao acessar a webcam:', error);
      }
    } else {
      console.error('getUserMedia não é suportado neste navegador.');
    }
  }

  captureImage(): void {
    const video = this.videoElement.nativeElement;
    const canvasEl = this.canvas.nativeElement;
    const context = canvasEl.getContext('2d');

    // Ajusta o tamanho do canvas para o tamanho do vídeo
    canvasEl.width = video.videoWidth;
    canvasEl.height = video.videoHeight;

    // Captura a imagem desenhando o frame atual do vídeo no canvas
    context?.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);

    // Converte o conteúdo do canvas para uma imagem em formato data URL
    this.capturedImage = canvasEl.toDataURL('image/png');
    console.log('Imagem capturada:', this.capturedImage);

    // Aqui você pode chamar o método para identificar o livro
    // this.identifyBook(this.capturedImage);
  }

  // Exemplo de método para enviar a imagem a uma API que identifica o livro
  identifyBook(imageData: string): void {
    // Use o HttpClient para enviar a imagem a uma API e tratar a resposta
    // Exemplo:
    // this.http.post('URL_DA_API', { image: imageData }).subscribe(response => { ... });
  }
}
