import { Component, Input, input } from '@angular/core';

@Component({
  selector: 'app-classification-cdd',
  standalone: false,
  templateUrl: './classification-cdd.component.html',
  styleUrl: './classification-cdd.component.scss'
})
export class ClassificationCDDComponent {
  @Input() data: any;

  getCDD(): string {
    return this.data?.meta?.CDD || 'N/A';
  }
}
