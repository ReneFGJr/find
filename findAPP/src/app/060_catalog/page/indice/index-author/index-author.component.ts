import { Router } from '@angular/router';
import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-index-author',
  standalone: false,
  templateUrl: './index-author.component.html',
  styleUrl: './index-author.component.scss',
})
export class IndexAuthorComponent {
  @Input() indexData: any = {};

  constructor(private router: Router) {}

  onSelect(item: any) {
    // aqui você decide o que fazer:
    // navegar, emitir evento, abrir modal etc.
    this.router.navigate(['/v/', item]);
  }
}
