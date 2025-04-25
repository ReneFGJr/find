import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { WaitingComponent } from '../waiting/waiting.component';
import { WebcamComponent } from '../../io/webcam/webcam.component';

@Component({
  selector: 'app-catalog',
  standalone: true,
  imports: [CommonModule, FormsModule, WaitingComponent, WebcamComponent],
  templateUrl: './catalog.component.html',
  styleUrl: './catalog.component.scss',
})
export class CatalogComponent {
  public status: string = '0';
  public isbn: string = '9786556990477';

  onSubmit() {
    console.log('ISBN enviado:', this.isbn);
    this.status = '9';
    // Implemente a lógica para tratar o envio do ISBN
  }

  onCancel() {
    console.log('Ação de cancelamento');
    this.status = '0';
    // Implemente a lógica para tratar o cancelamento
  }

  noISBN() {
    this.status = '1';
    // Implemente a lógica para marcar que a obra não possui ISBN
  }

  onIOwebcam()
    {
      this.status = '99';
    }
}
