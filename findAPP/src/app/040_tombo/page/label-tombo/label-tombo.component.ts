import { Component, ElementRef, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-label-tombo',
  standalone: false,
  templateUrl: './label-tombo.component.html',
  styleUrl: './label-tombo.component.scss',
})
export class LabelTomboComponent {
  form: FormGroup;
  loading = false;
  error: string | null = null;
  data: Array<any> | any;
  et: Array<any> | any;
  book: Array<any> | any;

  @ViewChild('cursoInput') cursoInput!: ElementRef<HTMLInputElement>;

  constructor(private fb: FormBuilder, private findService: FindService) {
    this.form = this.fb.group({
      tombo: ['', [Validators.required]],
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

  onClear(): void {
    let dt = {};
    this.findService.api_post('label/z', dt).subscribe({
      next: (res) => {
        console.log('Limpar:', res);
      },
    });
  }
  onPrintLabel()
    {
      window.open('https://www.ufrgs.br/find/v2/api/label', '_blank');
    }

  ngOnInit()
    {
      console.log('ngOnInit');
      this.onUpdateLabels();
    }

  onUpdateLabels() {
    let dt = {};
    this.findService.api_post('label/r', dt).subscribe({
      next: (res) => {
        this.et = res;
        console.log('Resumo:', res);
      },
    });
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
    console.log('Dados:', dt);
    this.findService.api_post('label/g', dt).subscribe({
      next: (res) => {
        console.log('Resultado:', res);
        this.data = res;
        this.clearAndFocus();
        this.onUpdateLabels();
        this.loading = false;
      },
      error: (err) => {
        this.loading = false;
        this.error = 'Não foi possível realizar a consulta.';
        console.error(err);
      },
    });
  }
}
