import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-work-form',
  standalone: false,
  templateUrl: './work-form.component.html',
  styleUrl: './work-form.component.scss',
})
export class WorkFormComponent {
  @Input() public work: any;
}
