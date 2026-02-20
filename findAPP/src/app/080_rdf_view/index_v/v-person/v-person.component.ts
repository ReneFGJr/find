import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-v-person',
  standalone: false,
  templateUrl: './v-person.component.html',
  styleUrl: './v-person.component.scss'
})
export class VPersonComponent {
  @Input() data: any;

  constructor() {}
}
