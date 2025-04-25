import { Component } from '@angular/core';

@Component({
  selector: 'app-catalog',
  standalone: false,
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

  onIOwebcam() {
    this.status = '99';
  }
}
