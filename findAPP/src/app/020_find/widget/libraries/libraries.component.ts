import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-libraries',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './libraries.component.html',
  styleUrl: './libraries.component.scss',
})
export class LibrariesComponent {
  @Input() libraries: any[] = []; // Input property to receive libraries data from parent component
}
