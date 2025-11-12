import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-user-new',
  standalone: false,
  templateUrl: './user-new.component.html',
  styleUrl: './user-new.component.scss',
})
export class UserNewComponent {
  userForm!: FormGroup;
  isSubmitting = false;

  constructor(
    private fb: FormBuilder,
    private findService: FindService,
    private router: Router
  ) {
    this.createForm();
  }

  createForm() {
    this.userForm = this.fb.group({
      us_nome: ['', Validators.required],
      us_email: ['', []],
      us_genero: ['', Validators.required],
      us_senha: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  isInvalid(field: string): boolean {
    const control = this.userForm.get(field);
    return !!(control && control.invalid && (control.dirty || control.touched));
  }

  onSubmit() {
    if (this.userForm.invalid) {
      this.userForm.markAllAsTouched();
      return;
    }

    this.isSubmitting = true;

    this.findService.api_post('users/new', this.userForm.value).subscribe({
      next: (data) => {
        console.log(data)
        alert('✅ Usuário cadastrado com sucesso!');
        this.router.navigate(['/users']);
      },
      error: (err: any) => {
        console.error('Erro ao salvar usuário:', err);
        alert('❌ Erro ao salvar usuário. Tente novamente.');
        this.isSubmitting = false;
      },
    });
  }

  /** 🔹 Gera uma senha aleatória numérica de 6 dígitos */
  gerarSenha() {
    const senha = Math.floor(100000 + Math.random() * 900000).toString();
    this.userForm.get('us_senha')?.setValue(senha);
  }
}
