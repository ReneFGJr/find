import { Component } from '@angular/core';
import { AbstractControl, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { SocialService } from '../../../010_core/service/social.service';

@Component({
  selector: 'app-user-auth',
  standalone: false,
  templateUrl: './user-auth.component.html',
  styleUrl: './user-auth.component.scss',
})
export class UserAuthComponent {
  activeTab: 'signin' | 'signup' | 'forgot' = 'signin';
  data: any; // Variável para armazenar dados de autenticação

  /** Formulários reativos */
  loginForm!: FormGroup;
  signupForm!: FormGroup;
  forgotForm!: FormGroup;

  isLoggedIn: boolean = false;

  /** Indicadores de loading e mensagens de sucesso/erro */
  isLoading = false;
  errorMessage: string | null = null;
  successMessage: string | null = null;

  constructor(
    private fb: FormBuilder,
    private socialService: SocialService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.buildForms();

  }

  private buildForms(): void {
    // Formulário de Login
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]],
    });

    // Formulário de Sign Up
    this.signupForm = this.fb.group(
      {
        name: ['', [Validators.required, Validators.minLength(3)]],
        email: ['', [Validators.required, Validators.email]],
        password: ['', [Validators.required, Validators.minLength(6)]],
        confirmPassword: ['', [Validators.required]],
      },
      { validator: this.passwordMatchValidator }
    );

    // Formulário de Forgot Password
    this.forgotForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  /** Validador customizado para conferir se password === confirmPassword */
  private passwordMatchValidator(group: AbstractControl) {
    const pass = group.get('password')?.value;
    const confirm = group.get('confirmPassword')?.value;
    return pass === confirm ? null : { mismatch: true };
  }

  /** Troca de abas */
  setTab(tab: 'signin' | 'signup' | 'forgot'): void {
    this.activeTab = tab;
    this.clearMessages();
  }

  private clearMessages(): void {
    this.errorMessage = null;
    this.successMessage = null;
    this.isLoading = false;
  }

  /** Submete login */
  onLogin(): void {
    if (this.loginForm.invalid) {
      this.loginForm.markAllAsTouched();
      return;
    }

    this.clearMessages();
    this.isLoading = true;
    const payload = this.loginForm.value;

    /* Chamada ao serviço de autenticação */
    this.socialService.signIn(payload.email, payload.password)
  }

  /** Submete Sign Up */
  onSignUp(): void {
    if (this.signupForm.invalid) {
      this.signupForm.markAllAsTouched();
      return;
    }

    this.clearMessages();
    this.isLoading = true;
    const { name, email, password, confirmPassword } = this.signupForm.value;
  }

  /** Submete Forgot Password */
  onForgot(): void {
    if (this.forgotForm.invalid) {
      this.forgotForm.markAllAsTouched();
      return;
    }

    this.clearMessages();
    this.isLoading = true;
    const { email } = this.forgotForm.value;
  }

  /** Faz logout e redireciona */
  onLogout(): void {
    this.socialService.logout();
    this.router.navigate(['/auth']); // ou rota de login
  }
}
