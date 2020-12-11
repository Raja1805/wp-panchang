<?php
/**
 * Panchang result.
 *
 * @package   Prokerala\WP\Panchang
 * @copyright 2020 Ennexa Technologies Private Limited
 * @license   https://www.gnu.org/licenses/gpl-2.0.en.html GPLV2
 * @link      https://api.prokerala.com
 */

/*
 * This file is part of Prokerala Panchang WordPress plugin
 *
 * Copyright (c) 2020 Ennexa Technologies Private Limited
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

// phpcs:disable VariableAnalysis, WordPress.WP.GlobalVariablesOverride.Prohibited

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="pk-panchang-wrapper">
    <?php if ( ! empty( $result ) ) : ?>
        <h2 class="pk-panchang-text-center">Today's Panchang</h2>
        <div class="pk-panchang-panchang-details">
            <span class="pk-panchang-block"><b>Location</b> : <?php echo $location; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
            <span class="pk-panchang-block"><b>Vaara</b> : <?php echo $result->vaara; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>

            <?php foreach ( [ 'sunrise', 'sunset', 'moonrise', 'moonset' ] as $key ):?>
                <span class="pk-panchang-block"><b><?php echo ucwords( $key ); // phpcs:ignore WordPress.Security.EscapeOutput ?></b> : <?php echo $result->$key->format( 'h:i A' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
            <?php endforeach;?>


            <?php foreach ([ 'nakshatra', 'tithi', 'karana', 'yoga' ] as $key):?>
                <hr>
                <span class="pk-panchang-block"><b><?php echo ucwords( $key ); // phpcs:ignore WordPress.Security.EscapeOutput ?></b></span>
                <?php foreach ( $result->$key as $value ) : ?>
                    <span class="pk-panchang-block">
                        <?php if ( 'tithi' === $key && 14 !== $value->id % 16 ):?>
                            <?php echo $value->paksha ?>
                        <?php endif;?>
                        <?php echo $value->name; // phpcs:ignore WordPress.Security.EscapeOutput ?> &mdash;
                        <?php echo $value->start->format( 'h:i A' ) . ' - ' . $value->end->format( 'h:i A' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
                <?php endforeach; ?>
            <?php endforeach;?>

            <hr>
            <table class="pk-panchang-table pk-panchang-table-responsive-sm">
                <tr class="pk-panchang-alert-success pk-panchang-text-center"><td colspan="2">Auspicious Timing</td></tr>
                <?php foreach ( $result->auspicious_period as $muhurat ) : ?>
                    <tr>
                        <td><?php echo ucwords( $muhurat->name ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td><td>
                            <?php foreach ( $muhurat->period as $period ) : ?>
                                <?php echo $period->start->format( 'h:i A' ); ?> - <?php echo $period->end->format( 'h:i A' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><br>

                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr class="pk-panchang-alert-danger pk-panchang-text-center"><td colspan="2">Inauspicious Timing</td></tr>
                <?php foreach ( $result->inauspicious_period as $muhurat ) : ?>
                    <tr>
                        <td><?php echo ucwords( $muhurat->name ); // phpcs:ignore WordPress.Security.EscapeOutput ?></td><td>
                            <?php foreach ( $muhurat->period as $period ) : ?>
                                <?php echo $period->start->format( 'h:i A' ); ?> - <?php echo $period->end->format( 'h:i A' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </div>
    <?php endif; ?>
    <!-- Following attribution is mandatory for free plans -->
    <p><small>Powered by <a href="https://www.prokerala.com/">Prokerala</a></small></p>
</div>
