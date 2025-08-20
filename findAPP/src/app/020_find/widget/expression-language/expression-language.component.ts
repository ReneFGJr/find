import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-expression-language',
  standalone: false,
  templateUrl: './expression-language.component.html',
  styleUrl: './expression-language.component.scss'
})
export class ExpressionLanguageComponent {
  @Input() public expressionLanguage: string = 'pt-BR'; // Default language
}
