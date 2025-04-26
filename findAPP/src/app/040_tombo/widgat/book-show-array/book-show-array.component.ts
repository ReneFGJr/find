import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-book-show-array',
  standalone: false,
  templateUrl: './book-show-array.component.html',
  styleUrl: './book-show-array.component.scss'
})
export class BookShowArrayComponent {
  @Input() data: any;
  @Input() type: string = 'author';
  @Input() label: string = '';
}
