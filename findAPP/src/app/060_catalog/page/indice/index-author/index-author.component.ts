import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-index-author',
  standalone: false,
  templateUrl: './index-author.component.html',
  styleUrl: './index-author.component.scss'
})
export class IndexAuthorComponent {
  @Input() indexData: any = {};

  get letters(): string[] {
    return Object.keys(this.indexData);
  }

  onSelect(item: any) {
    // aqui você decide o que fazer:
    // navegar, emitir evento, abrir modal etc.
    console.log('Selecionado:', item);
  }
}
