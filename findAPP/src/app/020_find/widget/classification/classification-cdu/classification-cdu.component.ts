import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-classification-cdu',
  standalone: false,
  templateUrl: './classification-cdu.component.html',
  styleUrl: './classification-cdu.component.scss'
})
export class ClassificationCDUComponent {
  @Input() data: any;
}
