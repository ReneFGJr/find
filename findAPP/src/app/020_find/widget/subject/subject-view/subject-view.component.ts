import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-subject-view',
  standalone: false,
  templateUrl: './subject-view.component.html',
  styleUrl: './subject-view.component.scss'
})
export class SubjectViewComponent {
  @Input() subject: any = [];
}
