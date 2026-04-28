                <?php
                // Exibe rádios para seleção de idioma
                $selectedLang = isset($lang) ? $lang : null;
                $first = true;
                if (isset($langs) && is_array($langs)) :
                    foreach ($langs as $l) :
                        $checked = '';
                        if ($selectedLang) {
                            if ($selectedLang == $l['lg_code']) {
                                $checked = 'checked';
                            }
                        } elseif ($first) {
                            $checked = 'checked';
                        }
                ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="lang" id="lang_<?= $l['lg_code']; ?>" value="<?= $l['lg_code']; ?>" <?= $checked; ?>>
                            <label class="form-check-label" for="lang_<?= $l['lg_code']; ?>">
                                <?= $l['lg_name']; ?>
                            </label>
                        </div>
                <?php
                        $first = false;
                    endforeach;
                endif;
                ?>