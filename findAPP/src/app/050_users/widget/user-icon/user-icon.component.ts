import { Component, EventEmitter, Input, Output } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-user-icon',
  standalone: false,
  templateUrl: './user-icon.component.html',
  styleUrl: './user-icon.component.scss',
})
export class UserIconComponent {
  img: string = 'assets/icons/login.svg';
  @Input() userName: string = 'Usuário';
  @Input() loged: boolean = false;
  @Output() logedChange = new EventEmitter<boolean>();

  constructor(private findService: FindService, private router: Router) {}

  login(): void {
    this.loged = true; // Simula que o usuário está logado
    this.logedChange.emit(this.loged);
  }

  logout(): void {
    this.loged = false; // Simula que o usuário está logado
    this.logedChange.emit(this.loged);
  }
}
