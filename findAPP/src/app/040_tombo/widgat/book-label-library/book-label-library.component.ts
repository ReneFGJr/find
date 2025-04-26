import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-book-label-library',
  standalone: false,
  templateUrl: './book-label-library.component.html',
  styleUrl: './book-label-library.component.scss'
})
export class BookLabelLibraryComponent {
  @Input() data: any;
}
