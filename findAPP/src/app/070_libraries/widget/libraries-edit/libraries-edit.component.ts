import { Component, inject, signal } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { finalize } from 'rxjs';
import { Library } from '../../../010_core/service/library.model';
import { LibraryService } from '../../../010_core/service/library.service';

@Component({
  selector: 'app-libraries-edit',
  standalone: false,
  templateUrl: './libraries-edit.component.html',
  styleUrl: './libraries-edit.component.scss',
})
export class LibrariesEditComponent {
  private fb = inject(FormBuilder);
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private api = inject(LibraryService);

  isEdit = signal(false);
  loading = signal(false);
  saving = signal(false);
  serverError = signal<string | null>(null);
  successMessage = signal<string | null>(null);

  form = this.fb.nonNullable.group({
    id_l: [0],
    l_name: ['', [Validators.required, Validators.minLength(3)]],
    l_code: ['', [Validators.required, Validators.maxLength(15)]],
    l_id: [0, [Validators.required, Validators.min(1)]],
    l_logo: ['', [Validators.maxLength(80)]],
    l_about: [''],
    l_visible: [1, [Validators.required]],
    l_net: [0, [Validators.required]],
  });

  ngOnInit(): void {
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.isEdit.set(true);
      const id = Number(idParam);
      this.loading.set(true);
      this.api
        .getById(id)
        .pipe(finalize(() => this.loading.set(false)))
        .subscribe({
          next: (lib) => this.form.patchValue(lib),
          error: (err) =>
            this.serverError.set(
              err?.error?.message ?? 'Falha ao carregar registro.'
            ),
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
    this.api
      .uploadLogo(file)
      .pipe(finalize(() => this.saving.set(false)))
      .subscribe({
        next: (res) => {
          this.form.patchValue({ l_logo: res.path });
          this.successMessage.set('Logo enviado com sucesso!');
          setTimeout(() => this.successMessage.set(null), 3000);
        },
        error: (err) =>
          this.serverError.set(
            err?.error?.message ?? 'Falha no upload do logo.'
          ),
      });
  }

  submit() {
    this.serverError.set(null);
    this.successMessage.set(null);

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const payload: Library = {
      ...this.form.getRawValue(),
      // garantindo 0/1 nos toggles (caso altere UI futuramente)
      l_visible: this.form.value.l_visible ? 1 : 0,
      l_net: this.form.value.l_net ? 1 : 0,
    };

    this.saving.set(true);
    const req$ = this.isEdit()
      ? this.api.update(payload.id_l!, payload)
      : this.api.create(payload);

    req$.pipe(finalize(() => this.saving.set(false))).subscribe({
      next: (res) => {
        this.successMessage.set('Registro salvo com sucesso!');
        // opcional: voltar para lista
        setTimeout(() => this.router.navigate(['/libraries']), 600);
      },
      error: (err) =>
        this.serverError.set(err?.error?.message ?? 'Falha ao salvar.'),
    });
  }

  get f() {
    return this.form.controls;
  }
}
