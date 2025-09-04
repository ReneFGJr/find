import { Component, inject, Input, signal } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-libraries-logo',
  standalone: false,
  templateUrl: './libraries-logo.component.html',
  styleUrl: './libraries-logo.component.scss',
})
export class LibrariesLogoComponent {
  @Input() library: number = 0;
  private fb = inject(FormBuilder);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  constructor(
    private findService: FindService // private router: Router
  ) {}

  isEdit = signal(false);
  loading = signal(false);
  saving = signal(false);
  serverError = signal<string | null>(null);
  successMessage = signal<string | null>(null);

  form = this.fb.nonNullable.group({
    id_l: [0],
    l_logo: ['', [Validators.maxLength(80)]],
  });

  ngOnInit(): void {
    const idParam = this.route.snapshot.paramMap.get('id');
    console.log('=====================', idParam);
    if (idParam) {
      this.isEdit.set(true);
      const id = Number(idParam);
      this.loading.set(true);
      this.findService.api_post(`library/get/id/${id}`, []).subscribe((res) => {
        console.log(res);
      });
    }
  }

  // Upload opcional do arquivo de logo
  onLogoFileChange(ev: Event) {
    const input = ev.target as HTMLInputElement;
    if (!input.files?.length) return;
    const file = input.files[0];

    // Opcional: validação simples de tamanho/ tipo
    if (file.size > 2_000_000) {
      // 2MB
      this.serverError.set('O arquivo de logo excede 2MB.');
      return;
    }

    this.saving.set(true);
    /*
    this.api
      .uploadLogo(file)
      .pipe(finalize(() => this.saving.set(false)))
      .subscribe({
        next: (res) => {
          this.form.patchValue({ l_logo: res.path });
          this.successMessage.set('Logo enviado com sucesso!');
          setTimeout(() => this.successMessage.set(null), 3000);
        },
      });
      */
  }

  submit() {
    this.serverError.set(null);
    this.successMessage.set(null);

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.saving.set(true);

    console.log('Salvando...');
    console.log(this.form.value);
    let dt = this.form.value;
    let id = this.form.value.id_l;
    this.findService.api_post(`library/save/${id}`, dt).subscribe((res) => {
      console.log(res);
    });
  }

  get f() {
    return this.form.controls;
  }
}
