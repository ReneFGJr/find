import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-expression-show',
  standalone: false,
  templateUrl: './expression-show.component.html',
  styleUrl: './expression-show.component.scss'
})
export class ExpressionShowComponent {
  @Input() public work: any;
}
