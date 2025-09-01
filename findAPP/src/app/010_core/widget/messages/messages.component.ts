import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-messages',
  standalone: false,
  templateUrl: './messages.component.html',
  styleUrl: './messages.component.scss'
})
export class MessagesComponent {
  @Input() messages: string = '';
  @Input() type: 'success' | 'error' | 'info' = 'info';
}
