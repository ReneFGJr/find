import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-book-status',
  standalone: false,
  templateUrl: './book-status.component.html',
  styleUrl: './book-status.component.scss'
})
export class BookStatusComponent {
  @Input() data: any;
}
