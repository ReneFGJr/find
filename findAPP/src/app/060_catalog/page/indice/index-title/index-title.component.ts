import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-index-title',
  standalone: false,
  templateUrl: './index-title.component.html',
  styleUrl: './index-title.component.scss',
})
export class IndexTitleComponent {
  @Input() indexData: any;

  constructor(private router: Router) {}

  onSelect(item: any) {
    // aqui você decide o que fazer:
    // navegar, emitir evento, abrir modal etc.
    alert(item);
    this.router.navigate(['/v/', item]);
  }

  activeLetter: string = '';

  ngOnInit() {
    if (this.indexData?.data?.length) {
      this.activeLetter = this.indexData.data[0].letter;
    }
  }

  setActiveLetter(letter: string) {
    this.activeLetter = letter;
  }
}
