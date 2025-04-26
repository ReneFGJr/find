import { FindService } from './../../../010_core/service/find.service';
import { Component, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-tombo',
  standalone: false,
  templateUrl: './tombo.component.html',
  styleUrl: './tombo.component.scss',
})
export class TomboComponent {
  form: FormGroup;
  loading = false;
  error: string | null = null;
  data: Array<any> | any;
  book: Array<any> | any;

  @ViewChild('cursoInput') cursoInput!: ElementRef<HTMLInputElement>;

  constructor(private fb: FormBuilder, private findService: FindService) {
    this.form = this.fb.group({
      tombo: ['', [Validators.required, Validators.pattern(/^\d+$/)]],
    });
  }

  ngAfterViewInit(): void {
    // dá foco ao input assim que a view estiver pronta
    this.cursoInput.nativeElement.focus();
  }

  /** Zera o valor do input e recoloca o foco nele */
  clearAndFocus(): void {
    this.form.reset();
    this.cursoInput.nativeElement.focus();
  }

  onSubmit() {
    if (this.form.invalid) {
      return;
    }

    this.loading = true;
    this.error = null;
    const tombo = this.form.value.tombo;
    const dt = {
      tomboID: tombo,
    };

    this.findService.api_post('tombo/v', dt).subscribe({
      next: (res) => {
        this.loading = false;
        this.data = res;
        this.clearAndFocus();

        if (this.data.item.i_identifier) {
          let dt = {
            isbn: this.data.item.i_identifier,
            lib: this.data.item.i_library,
          };
          this.findService
            .api_post('getIsbn', dt)
            .subscribe((res) => {
              this.book = res;
            });
        }
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Não foi possível realizar a consulta.';
        console.error(err);
      },
    });
  }
}
