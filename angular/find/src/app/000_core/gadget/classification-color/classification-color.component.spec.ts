import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ClassificationColorComponent } from './classification-color.component';

describe('ClassificationColorComponent', () => {
  let component: ClassificationColorComponent;
  let fixture: ComponentFixture<ClassificationColorComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ClassificationColorComponent]
    });
    fixture = TestBed.createComponent(ClassificationColorComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
