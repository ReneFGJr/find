import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-book-label',
  standalone: false,
  templateUrl: './book-label.component.html',
  styleUrl: './book-label.component.scss',
})
export class BookLabelComponent {
  @Input() data: any;
  show: boolean = false;
  ngOnInit() {
    if (this.data?.i_ln_1 != '') {
      this.show = true;
    } else {
      this.show = false;
    }
  }
}
