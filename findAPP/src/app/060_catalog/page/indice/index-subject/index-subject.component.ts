import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-index-subject',
  standalone: false,
  templateUrl: './index-subject.component.html',
  styleUrl: './index-subject.component.scss',
})
export class IndexSubjectComponent {
  @Input() indexData: any = {};

  constructor(private router: Router) {}

  onSelect(item: any) {
    // aqui você decide o que fazer:
    // navegar, emitir evento, abrir modal etc.
    this.router.navigate(['/v/', item]);
  }
}
