import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-tombo-show',
  standalone: false,
  templateUrl: './tombo-show.component.html',
  styleUrl: './tombo-show.component.scss'
})
export class TomboShowComponent {
  @Input() data: any;
}
