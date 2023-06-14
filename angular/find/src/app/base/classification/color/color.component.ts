import { Component } from '@angular/core';

@Component({
  selector: 'app-color',
  templateUrl: './color.component.html',
  styleUrls: ['./color.component.scss']
})
export class ColorComponent {
  colors = [
      { id: 1, color: '#FFFF00', colort: '#333', description:'Adulto nacional'},
      { id: 10, color: '#084d6d', colort: '#EEE', description: 'Novelas/Romance/Ficção' },
      ]
}
