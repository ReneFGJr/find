import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-book-label-color',
  standalone: false,
  templateUrl: './book-label-color.component.html',
  styleUrl: './book-label-color.component.scss',
})
export class BookLabelColorComponent {
  @Input() book: any;
}
